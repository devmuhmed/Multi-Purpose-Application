<script setup>
import {onMounted, ref, computed} from 'vue'

const appointments = ref([])

// const appointmentStatus = {'scheduled': 1, 'confirmed': 2, 'cancelled': 3}
const selectedStatus = ref()
const appointmentStatus = ref([])
const getAppointmentStatus = (status) => {
    axios.get('/api/appointments/status')
        .then((response) => {
            appointmentStatus.value = response.data
        })
}
const getAppointments = (status) => {
    selectedStatus.value = status
    const params = {}
    if (status) {
        params.status = status
    }
    axios.get('/api/appointments',{
        params: params,
    })
        .then((response) => {
            appointments.value = response.data
        })
}

const appointmentsCount = computed(() =>{
    return appointmentStatus.value.reduce((acc,value) => acc + value.count, 0)
})

onMounted(() => {
    getAppointments()
    getAppointmentStatus()
})
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <router-link to="/admin/appointments/create">
                                <button class="btn btn-primary">
                                    <i class="fa fa-plus-circle mr-1"></i> Add New Appointment
                                </button>
                            </router-link>
                        </div>
                        <div class="btn-group">
                            <button @click="getAppointments()" type="button" class="btn" :class="[typeof selectedStatus === 'undefined' ? 'btn-secondary' : 'btn-default']">
                                <span class="mr-1">All</span>
                                <span class="badge badge-pill badge-info">{{ appointmentsCount }}</span>
                            </button>

                            <button v-for="status in appointmentStatus" @click="getAppointments(status.value)" type="button" class="btn" :class="[selectedStatus === status.value ? 'btn-secondary' : 'btn-default']">
                                <span class="mr-1">{{ status.name }}</span>
                                <span class="badge badge-pill" :class="`badge-${status.color}`">{{ status.count }}</span>
                            </button>

<!--                            <button @click="getAppointments(appointmentStatus.confirmed)" type="button" class="btn btn-default">-->
<!--                                <span class="mr-1">Confirmed</span>-->
<!--                                <span class="badge badge-pill badge-success">0</span>-->
<!--                            </button>-->

<!--                            <button @click="getAppointments(appointmentStatus.cancelled)" type="button" class="btn btn-default">-->
<!--                                <span class="mr-1">Cancelled</span>-->
<!--                                <span class="badge badge-pill badge-danger">0</span>-->
<!--                            </button>-->
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Client Name</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
                                    <td>{{ index + 1 }}</td>
                                    <td> {{ appointment.client.first_name }} {{ appointment.client.last_name }}</td>
                                    <td>{{ appointment.start_time }}</td>
                                    <td>{{ appointment.end_time }}</td>
                                    <td>
                                        <span class="badge" :class="`badge-${appointment.status.color}`">{{ appointment.status.name }}</span>
                                    </td>
                                    <td>
                                        <router-link :to="`/admin/appointments/${appointment.id}/edit`">
                                            <i class="fa fa-edit mr-2"></i>
                                        </router-link>

                                        <a href="">
                                            <i class="fa fa-trash text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
