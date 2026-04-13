<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ plans: Array, trainers: Array });

const search = ref('');
const dialog = ref(false);
const editDialog = ref(false);
const editTarget = ref(null);

const filtered = computed(() =>
    props.plans.filter(p =>
        p.nombre.toLowerCase().includes(search.value.toLowerCase())
    )
);

const form = useForm({
    nombre:      '',
    descripcion: '',
    tipo:        'asistido',
    dias_semana: 3,
    trainer_id:  null,
});

const editForm = useForm({
    nombre:      '',
    descripcion: '',
    tipo:        'asistido',
    dias_semana: 3,
    trainer_id:  null,
    activo:      true,
});

const openEdit = (plan) => {
    editTarget.value = plan;
    editForm.nombre      = plan.nombre;
    editForm.descripcion = plan.descripcion;
    editForm.tipo        = plan.tipo;
    editForm.dias_semana = plan.dias_semana;
    editForm.trainer_id  = null;
    editForm.activo      = plan.activo;
    editDialog.value = true;
};

const submit = () => {
    form.post(route('training-plans.store'), {
        onSuccess: () => { dialog.value = false; form.reset(); },
    });
};

const submitEdit = () => {
    editForm.patch(route('training-plans.update', editTarget.value.id), {
        onSuccess: () => { editDialog.value = false; },
    });
};

const tipoColor = (tipo) => tipo === 'asistido' ? 'info' : 'secondary';
</script>

<template>
    <Head title="Planes de Entrenamiento" />

    <AuthenticatedLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Planes de Entrenamiento</h1>
                <p class="text-gray-400 text-sm mt-1">{{ plans.length }} planes registrados</p>
            </div>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="dialog = true">
                Nuevo Plan
            </v-btn>
        </div>

        <!-- Buscador -->
        <v-card color="#1f2937" rounded="lg" elevation="0" class="mb-4 pa-4">
            <v-text-field
                v-model="search"
                placeholder="Buscar por nombre..."
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
                    { title: 'Plan',         key: 'nombre' },
                    { title: 'Tipo',         key: 'tipo' },
                    { title: 'Días/semana',  key: 'dias_semana' },
                    { title: 'Entrenador',   key: 'trainer_name' },
                    { title: 'Estado',       key: 'activo' },
                    { title: 'Acciones',     key: 'actions', sortable: false },
                ]"
                :items="filtered"
                theme="dark"
                density="comfortable"
            >
                <template #item.nombre="{ item }">
                    <div class="font-medium text-white">{{ item.nombre }}</div>
                    <div class="text-gray-400 text-xs">{{ item.descripcion }}</div>
                </template>

                <template #item.tipo="{ item }">
                    <v-chip :color="tipoColor(item.tipo)" size="small" variant="tonal">
                        {{ item.tipo === 'asistido' ? 'Con entrenador' : 'Libre' }}
                    </v-chip>
                </template>

                <template #item.dias_semana="{ item }">
                    <span class="text-gray-300">{{ item.dias_semana }} días</span>
                </template>

                <template #item.activo="{ item }">
                    <v-chip :color="item.activo ? 'success' : 'secondary'" size="small" variant="tonal">
                        {{ item.activo ? 'Activo' : 'Inactivo' }}
                    </v-chip>
                </template>

                <template #item.actions="{ item }">
                    <v-btn icon="mdi-pencil" size="small" variant="text" color="warning" @click="openEdit(item)" />
                </template>
            </v-data-table>
        </v-card>

        <!-- Dialog nuevo plan -->
        <v-dialog v-model="dialog" max-width="500">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white">Nuevo Plan de Entrenamiento</v-card-title>
                <v-card-text class="pa-5 pt-2">
                    <v-text-field v-model="form.nombre" label="Nombre *" variant="outlined" density="comfortable" :error-messages="form.errors.nombre" color="primary" class="mb-3" />
                    <v-textarea v-model="form.descripcion" label="Descripción" variant="outlined" density="comfortable" rows="2" color="primary" class="mb-3" />
                    <v-select
                        v-model="form.tipo"
                        label="Tipo *"
                        :items="[{ title: 'Con entrenador', value: 'asistido' }, { title: 'Libre', value: 'libre' }]"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        class="mb-3"
                    />
                    <v-text-field v-model="form.dias_semana" label="Días por semana *" type="number" min="1" max="7" variant="outlined" density="comfortable" :error-messages="form.errors.dias_semana" color="primary" class="mb-3" />
                    <v-select
                        v-if="form.tipo === 'asistido'"
                        v-model="form.trainer_id"
                        label="Entrenador *"
                        :items="trainers"
                        item-title="name"
                        item-value="id"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.trainer_id"
                        color="primary"
                    />
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="dialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="form.processing" @click="submit">Crear</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog editar plan -->
        <v-dialog v-model="editDialog" max-width="500">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white">Editar Plan</v-card-title>
                <v-card-text class="pa-5 pt-2">
                    <v-text-field v-model="editForm.nombre" label="Nombre *" variant="outlined" density="comfortable" :error-messages="editForm.errors.nombre" color="primary" class="mb-3" />
                    <v-textarea v-model="editForm.descripcion" label="Descripción" variant="outlined" density="comfortable" rows="2" color="primary" class="mb-3" />
                    <v-select
                        v-model="editForm.tipo"
                        label="Tipo *"
                        :items="[{ title: 'Con entrenador', value: 'asistido' }, { title: 'Libre', value: 'libre' }]"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        class="mb-3"
                    />
                    <v-text-field v-model="editForm.dias_semana" label="Días por semana *" type="number" min="1" max="7" variant="outlined" density="comfortable" color="primary" class="mb-3" />
                    <v-select
                        v-if="editForm.tipo === 'asistido'"
                        v-model="editForm.trainer_id"
                        label="Entrenador"
                        :items="trainers"
                        item-title="name"
                        item-value="id"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        class="mb-3"
                    />
                    <v-switch v-model="editForm.activo" label="Plan activo" color="primary" inset />
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="editDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="editForm.processing" @click="submitEdit">Guardar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>
