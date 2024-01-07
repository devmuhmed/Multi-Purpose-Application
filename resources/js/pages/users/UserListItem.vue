<script setup>
import { formatDate } from "../../helper.js";
import {ref} from "vue";
import { useToaster } from "../../toastr.js";
import UserListItem from "./UserListItem.vue";

const toaster = useToaster();

defineProps({
    user: Object,
    index: Number,
})

const emit = defineEmits(['userDeleted', 'editUser']);
const userIdBeingDeleted = ref(null);

const confirmUserDeletion = (user) => {
    userIdBeingDeleted.value = user.id;
    $('#deleteUserModal').modal('show');
}

const deleteUser = () => {
    axios.delete(`/api/users/${userIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            toaster.success('User deleted successfully!')
            emit('userDeleted', userIdBeingDeleted.value)
        })
        .catch()
}

const editUser = (user) => {
    emit('editUser', user)
}
</script>
<template>
    <tr>
        <td> {{ ++index }} </td>
        <td> {{ user.name }} </td>
        <td> {{ user.email }} </td>
        <td> {{ formatDate(user.created_at) }} </td>
        <td> {{ user.role }} </td>
        <td>
            <a href="#" @click.prevent="editUser(user)">
                <i class="far fa-edit"></i>
            </a>
            <a href="#" @click.prevent="confirmUserDeletion(user)">
                <i class="fa fa-trash text-danger ml-2"></i>
            </a>
        </td>
    </tr>

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

