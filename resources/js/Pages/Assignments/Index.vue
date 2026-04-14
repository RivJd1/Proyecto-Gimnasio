<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    assignments: Array,
    clients:     Array,
    plans:       Array,
    trainers:    Array,
});

const dialog = ref(false);
const search = ref('');

const filtered = computed(() =>
    props.assignments.filter(a =>
        a.client_name.toLowerCase().includes(search.value.toLowerCase())
    )
);

const form = useForm({
    client_id:        null,
    training_plan_id: null,
    trainer_id:       null,
    fecha_inicio:     new Date().toISOString().split('T')[0],
    fecha_fin:        '',
    notas:            '',
});

const selectedPlan = computed(() =>
    props.plans.find(p => p.id === form.training_plan_id) ?? null
);

const submit = () => {
    form.post(route('assignments.store'), {
        onSuccess: () => { dialog.value = false; form.reset(); },
    });
};

const estadoColor = (estado) => estado === 'activo' ? 'success' : 'secondary';

const selectedClient = computed(() =>
    props.clients.find(c => c.id === form.client_id) ?? null
);
</script>

<template>
    <Head title="Asignaciones" />

    <AuthenticatedLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Asignaciones de Planes</h1>
                <p class="text-gray-400 text-sm mt-1">{{ assignments.length }} asignaciones registradas</p>
            </div>
            <v-btn color="primary" prepend-icon="mdi-account-check" @click="dialog = true">
                Nueva Asignación
            </v-btn>
        </div>

        <!-- Buscador -->
        <v-card color="#1f2937" rounded="lg" elevation="0" class="mb-4 pa-4">
            <v-text-field
                v-model="search"
                placeholder="Buscar por cliente..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                color="primary"
                clearable
            />
        </v-card>

        <!-- Tabla -->
        <v-card color="#1f2937" rounded="lg" elevation="0">
            <v-data-table
                :headers="[
                    { title: 'Cliente',      key: 'client_name' },
                    { title: 'Plan',         key: 'plan_nombre' },
                    { title: 'Entrenador',   key: 'trainer_name' },
                    { title: 'Inicio',       key: 'fecha_inicio' },
                    { title: 'Fin',          key: 'fecha_fin' },
                    { title: 'Estado',       key: 'estado' },
                ]"
                :items="filtered"
                theme="dark"
                density="comfortable"
            >
                <template #item.estado="{ item }">
                    <v-chip :color="estadoColor(item.estado)" size="small" variant="tonal">
                        {{ item.estado }}
                    </v-chip>
                </template>

                <template #item.fecha_fin="{ item }">
                    <span class="text-gray-300 text-sm">{{ item.fecha_fin ?? '—' }}</span>
                </template>
            </v-data-table>
        </v-card>

        <!-- Dialog nueva asignación -->
        <v-dialog v-model="dialog" max-width="520">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white">Asignar Plan a Cliente</v-card-title>
                <v-card-text class="pa-5 pt-2">

                    <!-- Alerta membresía vencida -->
                    <v-alert
                        v-if="selectedClient && !selectedClient.membresia_ok"
                        type="warning"
                        variant="tonal"
                        density="compact"
                        class="mb-4"
                        icon="mdi-alert"
                    >
                        Este cliente no tiene membresía activa. No se recomienda asignar un plan.
                    </v-alert>

                    <v-select
                        v-model="form.client_id"
                        label="Cliente *"
                        :items="clients"
                        item-title="name"
                        item-value="id"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.client_id"
                        color="primary"
                        class="mb-3"
                    />
                    <v-select
                        v-model="form.training_plan_id"
                        label="Plan de entrenamiento *"
                        :items="plans"
                        item-title="nombre"
                        item-value="id"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.training_plan_id"
                        color="primary"
                        class="mb-3"
                    />

                    <!-- Info del plan seleccionado -->
                    <div v-if="selectedPlan" class="pa-3 rounded-lg mb-3" style="background:#111827">
                        <div class="text-gray-400 text-xs">
                            Tipo: <span class="text-white">{{ selectedPlan.tipo === 'asistido' ? 'Con entrenador' : 'Libre' }}</span>
                            · Días/semana: <span class="text-white">{{ selectedPlan.dias_semana }}</span>
                        </div>
                    </div>

                    <v-select
                        v-if="selectedPlan?.tipo === 'asistido'"
                        v-model="form.trainer_id"
                        label="Entrenador"
                        :items="trainers"
                        item-title="name"
                        item-value="id"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        class="mb-3"
                    />

                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model="form.fecha_inicio"
                                label="Fecha inicio *"
                                type="date"
                                variant="outlined"
                                density="comfortable"
                                :error-messages="form.errors.fecha_inicio"
                                color="primary"
                            />
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                v-model="form.fecha_fin"
                                label="Fecha fin"
                                type="date"
                                variant="outlined"
                                density="comfortable"
                                color="primary"
                            />
                        </v-col>
                    </v-row>

                    <v-textarea v-model="form.notas" label="Notas" variant="outlined" density="comfortable" rows="2" color="primary" />
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="dialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="form.processing" @click="submit">Asignar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>
