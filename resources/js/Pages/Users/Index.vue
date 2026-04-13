<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ users: Array });

const dialog = ref(false);
const editDialog = ref(false);
const deactivateDialog = ref(false);
const editTarget = ref(null);
const deactivateTarget = ref(null);

const roleLabel = { admin: 'Administrador', receptionist: 'Recepcionista', trainer: 'Entrenador' };
const roleColor = { admin: 'warning', receptionist: 'info', trainer: 'success' };

const form = useForm({
    name:                  '',
    email:                 '',
    role:                  'receptionist',
    password:              '',
    password_confirmation: '',
});

const editForm = useForm({
    name:  '',
    email: '',
    role:  'receptionist',
});

const openEdit = (user) => {
    editTarget.value = user;
    editForm.name  = user.name;
    editForm.email = user.email;
    editForm.role  = user.role;
    editDialog.value = true;
};

const openDeactivate = (user) => {
    deactivateTarget.value = user;
    deactivateDialog.value = true;
};

const submit = () => {
    form.post(route('admin.users.store'), {
        onSuccess: () => { dialog.value = false; form.reset(); },
    });
};

const submitEdit = () => {
    editForm.patch(route('admin.users.update', editTarget.value.id), {
        onSuccess: () => { editDialog.value = false; },
    });
};

const confirmDeactivate = () => {
    useForm({}).delete(route('admin.users.destroy', deactivateTarget.value.id), {
        onSuccess: () => { deactivateDialog.value = false; },
    });
};

const roleItems = [
    { title: 'Administrador', value: 'admin' },
    { title: 'Recepcionista', value: 'receptionist' },
    { title: 'Entrenador',    value: 'trainer' },
];
</script>

<template>
    <Head title="Empleados" />

    <AuthenticatedLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Gestión de Empleados</h1>
                <p class="text-gray-400 text-sm mt-1">{{ users.length }} empleados registrados</p>
            </div>
            <v-btn color="primary" prepend-icon="mdi-account-plus" @click="dialog = true">
                Nuevo Empleado
            </v-btn>
        </div>

        <v-card color="#1f2937" rounded="lg" elevation="0">
            <v-data-table
                :headers="[
                    { title: 'Empleado',   key: 'name' },
                    { title: 'Rol',        key: 'role' },
                    { title: 'Estado',     key: 'active' },
                    { title: 'Registrado', key: 'created_at' },
                    { title: 'Acciones',   key: 'actions', sortable: false },
                ]"
                :items="users"
                theme="dark"
                density="comfortable"
            >
                <template #item.name="{ item }">
                    <div class="font-medium text-white">{{ item.name }}</div>
                    <div class="text-gray-400 text-xs">{{ item.email }}</div>
                </template>

                <template #item.role="{ item }">
                    <v-chip :color="roleColor[item.role]" size="small" variant="tonal">
                        {{ roleLabel[item.role] }}
                    </v-chip>
                </template>

                <template #item.active="{ item }">
                    <v-chip :color="item.active ? 'success' : 'secondary'" size="small" variant="tonal">
                        {{ item.active ? 'Activo' : 'Inactivo' }}
                    </v-chip>
                </template>

                <template #item.actions="{ item }">
                    <v-btn icon="mdi-pencil" size="small" variant="text" color="warning" @click="openEdit(item)" />
                    <v-btn
                        v-if="item.active"
                        icon="mdi-account-off"
                        size="small"
                        variant="text"
                        color="error"
                        @click="openDeactivate(item)"
                    />
                </template>
            </v-data-table>
        </v-card>

        <!-- Dialog nuevo empleado -->
        <v-dialog v-model="dialog" max-width="480">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white">Nuevo Empleado</v-card-title>
                <v-card-text class="pa-5 pt-2">
                    <v-text-field v-model="form.name" label="Nombre completo *" variant="outlined" density="comfortable" :error-messages="form.errors.name" color="primary" class="mb-3" />
                    <v-text-field v-model="form.email" label="Correo electrónico *" type="email" variant="outlined" density="comfortable" :error-messages="form.errors.email" color="primary" class="mb-3" />
                    <v-select v-model="form.role" label="Rol *" :items="roleItems" variant="outlined" density="comfortable" :error-messages="form.errors.role" color="primary" class="mb-3" />
                    <v-text-field v-model="form.password" label="Contraseña *" type="password" variant="outlined" density="comfortable" :error-messages="form.errors.password" color="primary" class="mb-3" />
                    <v-text-field v-model="form.password_confirmation" label="Confirmar contraseña *" type="password" variant="outlined" density="comfortable" color="primary" />
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="dialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="form.processing" @click="submit">Crear</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog editar empleado -->
        <v-dialog v-model="editDialog" max-width="480">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white">Editar Empleado</v-card-title>
                <v-card-text class="pa-5 pt-2">
                    <v-text-field v-model="editForm.name" label="Nombre completo *" variant="outlined" density="comfortable" :error-messages="editForm.errors.name" color="primary" class="mb-3" />
                    <v-text-field v-model="editForm.email" label="Correo electrónico *" type="email" variant="outlined" density="comfortable" :error-messages="editForm.errors.email" color="primary" class="mb-3" />
                    <v-select v-model="editForm.role" label="Rol *" :items="roleItems" variant="outlined" density="comfortable" color="primary" />
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="editDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="editForm.processing" @click="submitEdit">Guardar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog confirmar desactivación -->
        <v-dialog v-model="deactivateDialog" max-width="420">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 flex items-center gap-2">
                    <v-icon color="error">mdi-alert</v-icon>
                    <span class="text-white">Desactivar empleado</span>
                </v-card-title>
                <v-card-text class="pa-5 pt-0 text-gray-300">
                    ¿Está seguro que desea desactivar a
                    <strong class="text-white">{{ deactivateTarget?.name }}</strong>?
                    Esta acción le quitará el acceso al sistema.
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer />
                    <v-btn variant="tonal" color="secondary" @click="deactivateDialog = false">Cancelar</v-btn>
                    <v-btn color="error" @click="confirmDeactivate">Desactivar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>
