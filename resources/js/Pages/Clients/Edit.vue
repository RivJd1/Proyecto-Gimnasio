<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ client: Object });

const form = useForm({
    nombre:           props.client.nombre,
    apellido:         props.client.apellido,
    telefono:         props.client.telefono,
    email:            props.client.email ?? '',
    cedula:           props.client.cedula,
    fecha_nacimiento: props.client.fecha_nacimiento,
});

const confirmDialog = ref(false);
const changes = ref([]);

const getChanges = () => {
    const labels = {
        nombre:           'Nombre',
        apellido:         'Apellido',
        telefono:         'Teléfono',
        email:            'Correo',
        cedula:           'Cédula',
        fecha_nacimiento: 'Fecha de nacimiento',
    };
    return Object.keys(labels)
        .filter(key => form[key] !== (props.client[key] ?? ''))
        .map(key => ({
            campo:    labels[key],
            anterior: props.client[key] ?? '—',
            nuevo:    form[key],
        }));
};

const openConfirm = () => {
    changes.value = getChanges();
    if (changes.value.length === 0) return;
    confirmDialog.value = true;
};

const submit = () => {
    form.patch(route('clients.update', props.client.id), {
        onSuccess: () => { confirmDialog.value = false; },
    });
};
</script>

<template>
    <Head :title="`Editar — ${client.nombre} ${client.apellido}`" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <v-btn
                icon="mdi-arrow-left"
                variant="text"
                color="white"
                @click="router.visit(route('clients.show', client.id))"
            />
            <div>
                <h1 class="text-2xl font-bold text-white">Editar Cliente</h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ client.nombre }} {{ client.apellido }}
                </p>
            </div>
        </div>

        <v-card color="#1f2937" rounded="lg" elevation="0" class="pa-6 max-w-2xl">
            <v-row>
                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.nombre"
                        label="Nombre *"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.nombre"
                        color="primary"
                    />
                </v-col>
                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.apellido"
                        label="Apellido *"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.apellido"
                        color="primary"
                    />
                </v-col>
                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.telefono"
                        label="Teléfono *"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.telefono"
                        color="primary"
                    />
                </v-col>
                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.cedula"
                        label="Cédula *"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.cedula"
                        color="primary"
                    />
                </v-col>
                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.fecha_nacimiento"
                        label="Fecha de nacimiento *"
                        type="date"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.fecha_nacimiento"
                        color="primary"
                    />
                </v-col>
                <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.email"
                        label="Correo electrónico"
                        type="email"
                        variant="outlined"
                        density="comfortable"
                        :error-messages="form.errors.email"
                        color="primary"
                    />
                </v-col>
            </v-row>

            <div class="flex justify-end gap-3 mt-2">
                <v-btn
                    variant="tonal"
                    color="secondary"
                    @click="router.visit(route('clients.show', client.id))"
                >
                    Cancelar
                </v-btn>
                <v-btn
                    color="primary"
                    prepend-icon="mdi-content-save"
                    :disabled="form.processing"
                    @click="openConfirm"
                >
                    Guardar cambios
                </v-btn>
            </div>
        </v-card>

        <!-- Dialog de confirmación con comparativa -->
        <v-dialog v-model="confirmDialog" max-width="500">
            <v-card color="#1f2937" rounded="lg">
                <v-card-title class="pa-5 pb-2 text-white flex items-center gap-2">
                    <v-icon color="warning">mdi-alert-circle-outline</v-icon>
                    Confirmar cambios
                </v-card-title>
                <v-card-text class="pa-5 pt-2">
                    <p class="text-gray-400 text-sm mb-4">
                        Se modificarán los siguientes campos:
                    </p>
                    <div
                        v-for="change in changes"
                        :key="change.campo"
                        class="mb-3 pa-3 rounded-lg"
                        style="background: #111827"
                    >
                        <div class="text-gray-400 text-xs uppercase tracking-wide mb-1">
                            {{ change.campo }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-red-400 text-sm line-through">{{ change.anterior }}</span>
                            <v-icon size="14" color="gray">mdi-arrow-right</v-icon>
                            <span class="text-green-400 text-sm font-medium">{{ change.nuevo }}</span>
                        </div>
                    </div>
                </v-card-text>
                <v-card-actions class="pa-5 pt-0 gap-2">
                    <v-spacer />
                    <v-btn
                        variant="tonal"
                        color="secondary"
                        @click="confirmDialog = false"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="primary"
                        :loading="form.processing"
                        @click="submit"
                    >
                        Confirmar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>
