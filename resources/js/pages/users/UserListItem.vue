<script setup>
import {formatDate} from "../../helper.js";
import {ref, watch} from "vue";
import {useToaster} from "../../toastr.js";

const toaster = useToaster();

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
})

const emit = defineEmits(['userDeleted', 'editUser', 'toggleSelection', 'confirmUserDeletion']);

const roles = ref([
    {
        name: 'ADMIN',
        value: 1,
    },
    {
        name: 'USER',
        value: 2,
    }
]);

const changeRole = (user, role) => {
    axios.patch(`/api/users/${user.id}/change-role`, {
        role: role,
    })
        .then(() => {
            toaster.success('Role updated successfully!')
        })
}

const toggleSelection = (e) => {
    emit('toggleSelection', {user: props.user, status: e.target.checked})
}

watch(() => props.selectAll, () => {
    emit('toggleSelection', {user: props.user, status: props.selectAll})
})

</script>
<template>
    <tr>
        <td>
            <input type="checkbox" :checked="selectAll" @change="toggleSelection">
        </td>
        <td> {{ index + 1 }}</td>
        <td> {{ user.name }}</td>
        <td> {{ user.email }}</td>
        <td> {{ formatDate(user.created_at) }}</td>
        <td>
            <select class="form-control" @change="changeRole(user,$event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="user.role === role.name">{{
                        role.name
                    }}
                </option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser',user)">
                <i class="far fa-edit"></i>
            </a>
            <a href="#" @click.prevent="$emit('confirmUserDeletion',user.id)">
                <i class="fa fa-trash text-danger ml-2"></i>
            </a>
        </td>
    </tr>
</template>

