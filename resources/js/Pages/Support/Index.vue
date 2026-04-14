<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ providers: Array });

const page = usePage();
const isAdmin = computed(() => page.props.auth.user.role === 'admin');

const dialog = ref(false);
const editDialog = ref(false);
const deleteDialog = ref(false);
const editTarget = ref(null);
const deleteTarget = ref(null);
const search = ref('');

const filtered = computed(() =>
    props.providers.filter(p =>
        p.nombre.toLowerCase().includes(search.value.toLowerCase()) ||
        p.servicio.toLowerCase().includes(search.value.toLowerCase())
    )
);

const form = useForm({
    nombre:   '',
    telefono: '',
    email:    '',
    servicio: '',
    notas:    '',
});

const editForm = useForm({
    nombre:   '',
    telefono: '',
    email:    '',
    servicio: '',
    notas:    '',
});

const openEdit = (provider) => {
    editTarget.value = provider;
    editForm.nombre   = provider.nombre;
    editForm.telefono = provider.telefono;
    editForm.email    = provider.email ?? '';
    editForm.servicio = provider.servicio;
    editForm.notas    = provider.notas ?? '';
    editDialog.value  = true;
};

const openDelete = (provider) => {
    deleteTarget.value = provider;
    deleteDialog.value = true;
};

const submit = () => {
    form.post(route('support-providers.store'), {
        onSuccess: () => { dialog.value = false; form.reset(); },
    });
};

const submitEdit = () => {
    editForm.patch(route('support-providers.update', editTarget.value.id), {
        onSuccess: () => { editDialog.value = false; },
    });
};

const confirmDelete = () => {
    useForm({}).delete(route('support-providers.destroy', deleteTarget.value.id), {
        onSuccess: () => { deleteDialog.value = false; },
    });
};
</script>

<template>
    <Head title="Directorio de Soporte" />

    <AuthenticatedLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Directorio de Soporte</h1>
                <p class="text-gray-400 text-sm mt-1">Proveedores de mantenimiento y servicios</p>
            </div>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="dialog = true">
                Agregar Proveedor
            </v-btn>
        </div>

        <!-- Buscador -->
        <v-card color="#1f2937" rounded="lg" elevation="0" class="mb-4 pa-4">
            <v-text-field
                v-model="search"
                placeholder="Buscar por nombre o servicio..."
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                density="compact"
                hide-details
                color="primary"
                clearable
            />
        </v-card>

        <!-- Cards de proveedores -->
        <v-row>
            <v-col
                v-for="provider in filtered"
                :key="provider.id"
                cols="12" sm="6" lg="4"
            >
                <v-card color="#1f2937" rounded="lg" elevation="0">
                    <v-card-text class="pa-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <v-avatar color="primary" size="40">
                                    <v-icon color="white">mdi-wrench</v-icon>
                                </v-avatar>
                                <div>
                                    <div class="text-white font-semibold text-sm">{{ provider.nombre }}</div>
                                    <div class="text-gray-400 text-xs">{{ provider.servicio }}</div>
                                </div>
                            </div>
                            <v-chip :color="provider.activo ? 'success' : 'secondary'" size="x-small" variant="tonal">
                                {{ provider.activo ? 'Activo' : 'Inactivo' }}
                            </v-chip>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <v-icon size="14" color="gray">mdi-phone</v-icon>
                                <span class="text-gray-300 text-sm">{{ provider.telefono }}</span>
                            </div>
                            <div v-if="provider.email" class="flex items-center gap-2">
                                <v-icon size="14" color="gray">mdi-email</v-icon>
                                <span class="text-gray-300 text-sm">{{ provider.email }}</span>
                            </div>
                            <div v-if="provider.notas" class="flex items-start gap-2">
                                <v-icon size="14" color="gray" class="mt-0.5">mdi-note-text</v-icon>
                                <span class="text-gray-400 text-xs">{{ provider.notas }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <v-btn
                                size="small"
                                variant="tonal"
                                color="warning"
                                prepend-icon="mdi-pencil"
                                @click="openEdit(provider)"
                            >
                                Editar
                            </v-btn>
                            <v-btn
                                v-if="isAdmin"
                                size="small"
                                variant="tonal"
                                color="error"
                                prepend-icon="mdi-delete"
                                @click="openDelete(provider)"
                            >
                                Eliminar
                            </v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col v-if="filtered.length === 0" cols="12">
                <div class="text-center py-12 text-gray-500">
                    <v-icon size="48" class="mb-2">mdi-wrench-outline</v-icon>
                    <p>No se encontraron proveedores.</p>
                </div>
            </v-col>
        </v-row>

        <!-- Dialog agregar proveedor -->
        <v-dialog v-model="dialog" max-width="480">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white">Agregar Proveedor</v-card-title>
                <v-card-text class="pa-5 pt-2">
                    <v-text-field v-model="form.nombre" label="Nombre *" variant="outlined" density="comfortable" :error-messages="form.errors.nombre" color="primary" class="mb-3" />
                    <v-text-field v-model="form.servicio" label="Tipo de servicio *" variant="outlined" density="comfortable" :error-messages="form.errors.servicio" color="primary" class="mb-3" />
                    <v-text-field v-model="form.telefono" label="Teléfono *" variant="outlined" density="comfortable" :error-messages="form.errors.telefono" color="primary" class="mb-3" />
                    <v-text-field v-model="form.email" label="Correo electrónico" type="email" variant="outlined" density="comfortable" color="primary" class="mb-3" />
                    <v-textarea v-model="form.notas" label="Notas" variant="outlined" density="comfortable" rows="2" color="primary" />
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="dialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="form.processing" @click="submit">Agregar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog editar proveedor -->
        <v-dialog v-model="editDialog" max-width="480">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white">Editar Proveedor</v-card-title>
                <v-card-text class="pa-5 pt-2">
                    <v-text-field v-model="editForm.nombre" label="Nombre *" variant="outlined" density="comfortable" :error-messages="editForm.errors.nombre" color="primary" class="mb-3" />
                    <v-text-field v-model="editForm.servicio" label="Tipo de servicio *" variant="outlined" density="comfortable" :error-messages="editForm.errors.servicio" color="primary" class="mb-3" />
                    <v-text-field v-model="editForm.telefono" label="Teléfono *" variant="outlined" density="comfortable" :error-messages="editForm.errors.telefono" color="primary" class="mb-3" />
                    <v-text-field v-model="editForm.email" label="Correo electrónico" type="email" variant="outlined" density="comfortable" color="primary" class="mb-3" />
                    <v-textarea v-model="editForm.notas" label="Notas" variant="outlined" density="comfortable" rows="2" color="primary" />
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="editDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="editForm.processing" @click="submitEdit">Guardar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog confirmar eliminación -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 flex items-center gap-2">
                    <v-icon color="error">mdi-delete-alert</v-icon>
                    <span class="text-white">Eliminar proveedor</span>
                </v-card-title>
                <v-card-text class="pa-5 pt-0 text-gray-300">
                    ¿Eliminar a <strong class="text-white">{{ deleteTarget?.nombre }}</strong> del directorio? Esta acción no se puede deshacer.
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="deleteDialog = false">Cancelar</v-btn>
                    <v-btn color="error" @click="confirmDelete">Eliminar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>
