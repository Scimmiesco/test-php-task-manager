<script setup>
defineProps({
    show: Boolean,
    form: Object,
});

const emit = defineEmits(["close", "submit"]);
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/40"
                @click="emit('close')"
            ></div>

            <!-- Modal content -->
            <div
                class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6"
            >
                <h2 class="text-lg font-semibold mb-4">Create Project</h2>

                <form
                    @submit.prevent="emit('submit')"
                    class="flex flex-col gap-3"
                >
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Project name..."
                        required
                        class="border border-neutral-300 rounded-md px-3 py-2"
                    />

                    <div class="flex justify-end gap-2 mt-2">
                        <button
                            type="button"
                            @click="emit('close')"
                            class="px-4 py-2 rounded-md bg-neutral-200 hover:bg-neutral-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
