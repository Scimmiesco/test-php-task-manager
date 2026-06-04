<script setup>
import { ref, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import { Funnel } from "@lucide/vue";

const props = defineProps({
    tasks: Array,
    projects: Array,
    currentProjectId: [String, Number],
});

// Local state for drag and drop
const localTasks = ref([...props.tasks]);
const selectedProjectId = ref(props.currentProjectId || "");

watch(
    () => props.currentProjectId,
    (id) => {
        selectedProjectId.value = id || "";
    },
);

// Sync local tasks if props change (e.g. after adding/deleting)
watch(
    () => props.tasks,
    (newTasks) => {
        localTasks.value = [...newTasks];
    },
    { deep: true },
);

const form = useForm({
    name: "",
    project_id: selectedProjectId.value,
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
    <div
        class="flex flex-col gap-2 max-w-5xl mx-auto p-2 bg-neutral-200 min-h-screen"
    >
        <h1 class="mx-auto text-4xl font-mono tracking-wider font-bold p-1">
            The Task Manager
        </h1>

        <div class="">
            <label class="flex gap-2 px-2 py-1"
                ><Funnel /> Filter by Project
            </label>
            <select @change="filterProject" :value="selectedProjectId" class="">
                <option value="">All Projects</option>
                <option
                    v-for="project in projects"
                    :key="project.id"
                    :value="project.id"
                >
                    {{ project.name }}
                </option>
            </select>
        </div>

        <form @submit.prevent="addTask" class="flex gap-2">
            <input
                v-model="form.name"
                type="text"
                placeholder="New Task Name..."
                required
                class="flex-1"
            />
            <button type="submit" :disabled="form.processing" class="">
                Add Task
            </button>
        </form>

        <ul
            class="flex flex-col gap-2 items-center p-2 justify-center bg-neutral-100 min-h-36 rounded-md border-dashed border border-neutral-600"
        >
            <li v-if="localTasks.length === 0" class="">
                No tasks found. Create your first!
            </li>

            <li
                class="bg-neutral-200 w-full rounded-md px-2 py-1"
                v-for="task in localTasks"
                v-else
            >
                <span> {{ task.project?.name ?? "No project" }}</span>
                {{ task.name }}
            </li>
        </ul>
    </div>
</template>
