<template>
  <div class="rounded-lg bg-white shadow p-2">
    <div class="flex text-lg">
      <div class="font-semibold">
        <router-link :to="`/projects/${task.goal.project_id}/goals/${task.goal.id}`" class="text-blue-700 hover:text-blue-500">{{ task.goal.project.title }}</router-link>
      </div>
      <div class="ml-4 font-semibold text-orange-600" v-if="task.status == 'in_progress'">
        In Progress
      </div>
      <div class="ml-4 font-semibold text-red-600" v-if="task.status == 'not_started'">
        Not Started
      </div>
      <div class="ml-4 font-semibold text-gray-400" v-if="task.status == 'unsigned'">
        No Status
      </div>
      <div class="ml-4 font-semibold text-green-600" v-if="task.status == 'complete'">
        Completed
      </div>
    </div>
    <div class="text-gray-900">
      {{ task.body }}
    </div>
  </div>
</template>

<script>
export default {
  props: ['task']
}
</script>
