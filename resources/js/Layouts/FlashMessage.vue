<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success');

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            message.value = flash.success;
            type.value    = 'success';
            show.value    = true;
        } else if (flash?.error) {
            message.value = flash.error;
            type.value    = 'error';
            show.value    = true;
        }
    },
    { immediate: true, deep: true }
);

const icon = computed(() => ({
    success: 'mdi-check-circle',
    error:   'mdi-alert-circle',
    warning: 'mdi-alert',
    info:    'mdi-information',
}[type.value]));
</script>

<template>
    <v-snackbar
        v-model="show"
        :timeout="3500"
        location="bottom right"
        rounded="lg"
        :color="type"
        variant="tonal"
        elevation="4"
        min-width="300"
    >
        <div class="flex items-center gap-2">
            <v-icon :icon="icon" size="20" />
            <span class="text-sm font-medium">{{ message }}</span>
        </div>

        <template #actions>
            <v-btn
                icon="mdi-close"
                size="x-small"
                variant="text"
                @click="show = false"
            />
        </template>
    </v-snackbar>
</template>
