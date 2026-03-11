<script setup lang="ts">
import type { Student } from '../types/student'

type Props = {
  students: Student[]
  isLoading: boolean
  errorMessage: string
}

defineProps<Props>()

const emit = defineEmits<{
  (e: 'create'): void
  (e: 'edit', student: Student): void
  (e: 'delete', student: Student): void
}>()

const formatDate = (isoDate: string) => {
  const date = new Date(isoDate)
  if (Number.isNaN(date.getTime())) {
    return isoDate
  }
  return date.toLocaleDateString()
}
</script>

<template>
  <section class="card">
    <div class="header">
      <button class="primary" type="button" @click="emit('create')">Create Student</button>
    </div>

    <div v-if="isLoading" class="status">Loading students...</div>
    <div v-else-if="errorMessage" class="status error">{{ errorMessage }}</div>

    <div v-else class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Id</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Birth Date</th>
            <th>Address</th>
            <th>Modify</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in students" :key="student.id">
            <td>{{ student.id }}</td>
            <td>{{ student.fullName }}</td>
            <td>{{ student.age }}</td>
            <td>{{ student.gender }}</td>
            <td>{{ formatDate(student.birthDate) }}</td>
            <td>{{ student.address ?? '-' }}</td>
            <td>
              <button class="ghost" type="button" @click="emit('edit', student)">Modify</button>
            </td>
            <td>
              <button class="danger" type="button" @click="emit('delete', student)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<style scoped>
.card {
  width: min(1100px, 100%);
  background: #ffffff;
  border-radius: 18px;
  padding: 28px 24px;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
  display: grid;
  gap: 20px;
  justify-items: center;
}

.header {
  display: flex;
  justify-content: center;
  width: 100%;
}

.table-wrap {
  width: 100%;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 860px;
}

th,
td {
  padding: 12px 14px;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

th {
  background: #f8fafc;
  font-weight: 600;
}

.status {
  font-weight: 600;
}

.error {
  color: #b91c1c;
}
</style>
