<script setup>
import {computed, onMounted, reactive, ref, watch} from "vue";
import {Form, Field} from "vee-validate";
import * as yup from 'yup';
import { useToaster } from "../../toastr.js";
import UserListItem from "./UserListItem.vue";
import { debounce } from "lodash";
import { Bootstrap4Pagination} from "laravel-vue-pagination";

const toaster = useToaster();
const users = ref({'data': []});
const editing = ref(false);
const formValues = ref({});
const form = ref(null);


const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(6),
})

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().nullable().min(6),
})

const getUsers = (page = 1) => {
    axios.get(`/api/users?page=${page}`)
    .then( (response) => {
        users.value = response.data
        selectedUsers.value = []
        selectAll.value = false
    })
    .catch()
}

const createUser = (values, {resetForm, setErrors}) => {
    axios.post('/api/users',values)
    .then((response) => {
        $('#userFormModal').modal('hide');
        users.value.data.unshift(response.data);
        resetForm();
        toaster.success('User created successfully')
    })
    .catch((error) => {
        if(error.response.data.errors){
            setErrors(error.response.data.errors);
        }
    })
}

const addUser = () => {
    editing.value = false;
    $('#userFormModal').modal('show');
    form.value.resetForm();
}
const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $('#userFormModal').modal('show');
    form.value.setValues(user);
}

const updateUser = (values, {setErrors}) => {
    axios.put(`/api/users/${values.id}`, values)
        .then((response) => {
            const index = users.value.data.findIndex(user => user.id === response.data.id);
            users.value[index] = response.data; // Fix the typo here
            $('#userFormModal').modal('hide');
            toaster.success('User updated successfully')
        })
        .catch((error) => {
            setErrors(error.response.data.errors);
            console.log(error);
        })
};


const handleSubmit = (values, actions) => {
    editing.value ? updateUser(values, actions) : createUser(values, actions)
}

const useSchema = computed(() => {
    return editing.value ? editUserSchema : createUserSchema
})

const searchQuery = ref(null)
const search = () => {
    axios.get('/api/users/search', {
        params: {
            query: searchQuery.value
        }
    })
    .then((response) => {
        users.value = response.data

    })
    .catch((error) => {
        console.log(error);
    })
}

const selectAll = ref(false)
const selectedUsers = ref([])
const toggleSelection = ({user,status}) => {
    const index = selectedUsers.value.indexOf(user.id)
    if(index === -1 && status){
        selectedUsers.value.push(user.id)
    }else {
        selectedUsers.value.splice(index, 1)
    }
}

const userIdBeingDeleted = ref(null);

const confirmUserDeletion = (id) => {
    userIdBeingDeleted.value = id;
    $('#deleteUserModal').modal('show');
}

const deleteUser = () => {
    axios.delete(`/api/users/${userIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            toaster.success('User deleted successfully!')
            users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value)
        })
        .catch()
}


const bulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value
        }
    })
        .then(response => {
            users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id))
            selectedUsers.value = []
            selectAll.value = false
            toaster.success(response.data.message)
        })
}

watch(searchQuery, debounce(() => {
    search()
},300))

onMounted(() => {
    getUsers();
})
</script>


<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button @click="addUser" type="button" class="mb-2 btn btn-primary">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add New User
                    </button>
                    <div v-if="selectedUsers.length > 0">
                        <button @click="bulkDelete" type="button" class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2">Selected {{ selectedUsers.length }} users</span>
                    </div>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="search..."/>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" v-model="selectAll" />
                                </th>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                            <UserListItem v-for="(user, index) in users.data"
                                          :key="user.id"
                                          :user=user
                                          :index=index
                                          @edit-user="editUser"
                                          @user-deleted="userDeleted"
                                          @toggle-selection="toggleSelection"
                                          :select-all="selectAll"
                                          @confirm-user-deletion="confirmUserDeletion"
                            />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center ">No results found...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination
                :data="users"
                @pagination-change-page="getUsers"
            />
        </div>
    </div>

    <!-- Create or Edit User Modal -->
<div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <span v-if="editing">Edit User</span>
                    <span v-else>Add New User</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <Form ref="form" @submit="handleSubmit" :validation-schema="useSchema" v-slot="{ errors }" :initial-values="formValues">
                <div class="modal-body">

                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control " :class="{'is-invalid' : errors.name}" id="name"
                                   aria-describedby="nameHelp" placeholder="Enter full name" />
                            <span class="invalid-feedback">{{errors.name}}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control " :class="{'is-invalid' : errors.email}" id="email"
                                   aria-describedby="nameHelp" placeholder="Enter email" />
                            <span class="invalid-feedback">{{errors.email}}</span>
                        </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <Field name="password" type="password" class="form-control " :class="{'is-invalid' : errors.password}" id="password"
                               aria-describedby="nameHelp" placeholder="Enter password" />
                        <span class="invalid-feedback">{{errors.password}}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </Form>
        </div>
    </div>
</div>

    <!-- Confirm Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteUser" type="button" class="btn btn-primary">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
