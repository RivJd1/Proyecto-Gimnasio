<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ clients: Array });

const search = ref('');
const filterEstado = ref('todos');

const estadoOptions = [
    { title: 'Todos', value: 'todos' },
    { title: 'Activos', value: 'activo' },
    { title: 'Inactivos', value: 'inactivo' },
];

const filtered = computed(() => {
    return props.clients.filter(c => {
        const matchSearch =
            `${c.nombre} ${c.apellido}`.toLowerCase().includes(search.value.toLowerCase()) ||
            c.cedula?.includes(search.value) ||
            c.telefono?.includes(search.value);
        const matchEstado = filterEstado.value === 'todos' || c.estado === filterEstado.value;
        return matchSearch && matchEstado;
    });
});

const estadoColor = (estado, membresiaEstado) => {
    if (estado === 'inactivo') return 'secondary';
    if (membresiaEstado === 'vencida') return 'error';
    return 'success';
};

const estadoLabel = (estado, membresiaEstado) => {
    if (estado === 'inactivo') return 'Inactivo';
    if (membresiaEstado === 'vencida') return 'En mora';
    return 'Activo';
};

const goToClient = (client) => {
    router.visit(route('clients.show', client.id));
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Clientes</h1>
                <p class="text-gray-400 text-sm mt-1">{{ clients.length }} clientes registrados</p>
            </div>
            <v-btn
                color="primary"
                prepend-icon="mdi-account-plus"
                :href="route('clients.create')"
            >
                Nuevo Cliente
            </v-btn>
        </div>

        <!-- Filtros -->
        <v-card color="#1f2937" rounded="lg" elevation="0" class="mb-4 pa-4">
            <v-row align="center">
                <v-col cols="12" sm="7">
                    <v-text-field
                        v-model="search"
                        placeholder="Buscar por nombre, cédula o teléfono..."
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        density="compact"
                        hide-details
                        color="primary"
                        clearable
                    />
                </v-col>
                <v-col cols="12" sm="5">
                    <v-btn-toggle
                        v-model="filterEstado"
                        mandatory
                        color="primary"
                        variant="outlined"
                        density="compact"
                        divided
                    >
                        <v-btn
                            v-for="opt in estadoOptions"
                            :key="opt.value"
                            :value="opt.value"
                            size="small"
                        >
                            {{ opt.title }}
                        </v-btn>
                    </v-btn-toggle>
                </v-col>
            </v-row>
        </v-card>

        <!-- Tabla -->
        <v-card color="#1f2937" rounded="lg" elevation="0">
            <v-data-table
                :headers="[
                    { title: 'Cliente',    key: 'nombre' },
                    { title: 'Cédula',     key: 'cedula' },
                    { title: 'Teléfono',   key: 'telefono' },
                    { title: 'Plan',       key: 'plan' },
                    { title: 'Estado',     key: 'estado' },
                    { title: 'Acciones',   key: 'actions', sortable: false },
                ]"
                :items="filtered"
                theme="dark"
                density="comfortable"
                hover
            >
                <!-- Nombre -->
                <template #item.nombre="{ item }">
                    <div class="font-medium text-white">{{ item.nombre }} {{ item.apellido }}</div>
                    <div class="text-gray-400 text-xs">{{ item.email }}</div>
                </template>

                <!-- Plan -->
                <template #item.plan="{ item }">
                    <span class="text-gray-300 text-sm">{{ item.plan ?? '—' }}</span>
                </template>

                <!-- Estado -->
                <template #item.estado="{ item }">
                    <v-chip
                        :color="estadoColor(item.estado, item.membresia_estado)"
                        size="small"
                        variant="tonal"
                    >
                        {{ estadoLabel(item.estado, item.membresia_estado) }}
                    </v-chip>
                </template>

                <!-- Acciones -->
                <template #item.actions="{ item }">
                    <v-btn
                        icon="mdi-eye"
                        size="small"
                        variant="text"
                        color="info"
                        :href="route('clients.show', item.id)"
                    />
                    <v-btn
                        icon="mdi-pencil"
                        size="small"
                        variant="text"
                        color="warning"
                        :href="route('clients.edit', item.id)"
                    />
                </template>

                <!-- Empty state -->
                <template #no-data>
                    <div class="text-center py-10 text-gray-500">
                        <v-icon size="48" class="mb-2">mdi-account-off-outline</v-icon>
                        <p>No se encontraron clientes.</p>
                    </div>
                </template>
            </v-data-table>
        </v-card>
    </AuthenticatedLayout>
</template>
