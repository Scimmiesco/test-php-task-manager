<script setup>
import { ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps({
    tasks: Array,
    projects: Array,
    currentProjectId: [String, Number],
});

// Local state for drag and drop
const localTasks = ref([...props.tasks]);

// Sync local tasks if props change (e.g. after adding/deleting)
watch(
    () => props.tasks,
    (newTasks) => {
        localTasks.value = [...newTasks];
    },
    { deep: true },
);

// Form for new task
const form = useForm({
    name: "",
    project_id: props.currentProjectId || "",
});

const addTask = () => {
    form.post(route("tasks.store"), {
        onSuccess: () => form.reset("name"),
    });
};

const deleteTask = (id) => {
    if (confirm("Are you sure you want to delete this task?")) {
        router.delete(route("tasks.destroy", id));
    }
};

const updateTask = (task) => {
    const newName = prompt("Update task name:", task.name);
    if (newName && newName !== task.name) {
        router.put(route("tasks.update", task.id), { name: newName });
    }
};

// Handle Drag End event to update priorities
const onDragEnd = () => {
    const reorderedTasks = localTasks.value.map((task, index) => ({
        id: task.id,
        priority: index + 1,
    }));

    router.post(
        route("tasks.reorder"),
        { tasks: reorderedTasks },
        {
            preserveScroll: true,
        },
    );
};

const filterProject = (event) => {
    const projectId = event.target.value;
    router.get(
        route("tasks.index"),
        projectId ? { project_id: projectId } : {},
        {
            preserveState: true,
        },
    );
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Task Manager</h1>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2"
                >Filter by Project (Bonus)</label
            >
            <select
                @change="filterProject"
                v-model="form.project_id"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">All Projects (or Create Unassigned)</option>
                <option
                    v-for="project in projects"
                    :key="project.id"
                    :value="project.id"
                >
                    {{ project.name }}
                </option>
            </select>
        </div>

        <form @submit.prevent="addTask" class="flex gap-4 mb-8">
            <input
                v-model="form.name"
                type="text"
                placeholder="New Task Name..."
                required
                class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
            <button
                type="submit"
                :disabled="form.processing"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
            >
                Add Task
            </button>
        </form>

        <div class="bg-white rounded-lg shadow">
            <draggable
                v-model="localTasks"
                @end="onDragEnd"
                item-key="id"
                class="divide-y divide-gray-200"
            >
                <template #item="{ element, index }">
                    <div
                        class="p-4 flex items-center justify-between hover:bg-gray-50 cursor-move group"
                    >
                        <div class="flex items-center gap-4">
                            <span class="text-gray-400 font-mono"
                                >#{{ index + 1 }}</span
                            >
                            <span class="font-medium text-gray-900">{{
                                element.name
                            }}</span>
                            <span
                                class="text-xs text-gray-500"
                                v-if="element.project_id"
                            >
                                (Project ID: {{ element.project_id }})
                            </span>
                        </div>

                        <div
                            class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                            <button
                                @click="updateTask(element)"
                                class="text-blue-600 hover:text-blue-800 text-sm"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteTask(element.id)"
                                class="text-red-600 hover:text-red-800 text-sm"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </template>
            </draggable>

            <div
                v-if="localTasks.length === 0"
                class="p-8 text-center text-gray-500"
            >
                No tasks found. Create one above!
            </div>
        </div>
    </div>
</template>
