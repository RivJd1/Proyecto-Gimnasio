<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    overduePayments: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value.role === 'admin');

const statCards = computed(() => [
    {
        label: 'Clientes Activos',
        value: props.stats.clientes_activos,
        icon: 'mdi-account-check',
        color: 'success',
    },
    {
        label: 'Clientes Inactivos',
        value: props.stats.clientes_inactivos,
        icon: 'mdi-account-off',
        color: 'secondary',
    },
    {
        label: 'Membresías Activas',
        value: props.stats.membresias_activas,
        icon: 'mdi-card-account-details',
        color: 'info',
    },
    {
        label: 'Pagos Vencidos',
        value: props.stats.pagos_vencidos,
        icon: 'mdi-alert-circle',
        color: 'error',
    },
]);
</script>

<template>
    <Head title="Panel General" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Panel General</h1>
            <p class="text-gray-400 text-sm mt-1">
                Bienvenido, {{ user.name }} —
                {{ new Date().toLocaleDateString('es-HN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
            </p>
        </div>

        <!-- Alerta permanente de mora -->
        <v-alert
            v-if="overduePayments.length > 0"
            type="error"
            variant="tonal"
            prominent
            class="mb-6"
            icon="mdi-alert-circle"
        >
            <v-alert-title class="font-bold">
                ⚠️ {{ overduePayments.length }} cliente(s) con pago vencido
            </v-alert-title>
            <span class="text-sm">Revisar y gestionar los pagos pendientes a la brevedad.</span>
        </v-alert>

        <!-- Stats Cards -->
        <v-row class="mb-6">
            <v-col
                v-for="card in statCards"
                :key="card.label"
                cols="12" sm="6" lg="3"
            >
                <v-card color="#1f2937" rounded="lg" elevation="0">
                    <v-card-text class="pa-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-gray-400 text-xs mb-1">{{ card.label }}</div>
                                <div class="text-white text-3xl font-bold">{{ card.value }}</div>
                            </div>
                            <v-icon :color="card.color" size="40">{{ card.icon }}</v-icon>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Tabla de pagos vencidos -->
        <v-card color="#1f2937" rounded="lg" elevation="0">
            <v-card-title class="pa-5 pb-0 flex items-center gap-2">
                <v-icon color="error">mdi-alert-circle-outline</v-icon>
                <span class="text-white text-base font-semibold">Clientes con Pagos Vencidos</span>
                <v-spacer />
                <v-chip color="error" size="small">{{ overduePayments.length }} pendientes</v-chip>
            </v-card-title>

            <v-card-text class="pa-5 pt-4">
                <v-data-table
                    v-if="overduePayments.length > 0"
                    :headers="[
                        { title: 'Cliente',          key: 'client_name' },
                        { title: 'Plan',             key: 'plan' },
                        { title: 'Monto',            key: 'monto' },
                        { title: 'Vencimiento',      key: 'fecha_vencimiento' },
                        { title: 'Días de atraso',   key: 'dias_atraso' },
                    ]"
                    :items="overduePayments"
                    density="comfortable"
                    theme="dark"
                    hide-default-footer
                >
                    <template #item.monto="{ item }">
                        <span class="text-red-400 font-semibold">L. {{ Number(item.monto).toFixed(2) }}</span>
                    </template>

                    <template #item.dias_atraso="{ item }">
                        <v-chip
                            :color="item.dias_atraso > 15 ? 'error' : 'warning'"
                            size="small"
                        >
                            {{ item.dias_atraso }} días
                        </v-chip>
                    </template>

                    <template #item.fecha_vencimiento="{ item }">
                        <span class="text-gray-300 text-sm">{{ item.fecha_vencimiento }}</span>
                    </template>
                </v-data-table>

                <div v-else class="text-center py-8 text-gray-500">
                    <v-icon size="40" class="mb-2">mdi-check-circle-outline</v-icon>
                    <p>No hay pagos vencidos. ¡Todo al día!</p>
                </div>
            </v-card-text>
        </v-card>
    </AuthenticatedLayout>
</template>
