<script setup lang="ts">
import { onMounted, ref } from 'vue'

type Student = {
  id: number
  firstName: string
  lastName: string
  gender: string
  birthDate: string
  address: string | null
}

const students = ref<Student[]>([])
const isLoading = ref(true)
const errorMessage = ref('')

const loadStudents = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const baseUrl = import.meta.env.VITE_API_BASE_URL ?? ''
    const response = await fetch(`${baseUrl}/students`)
    if (!response.ok) {
      throw new Error(`Request failed: ${response.status}`)
    }
    students.value = (await response.json()) as Student[]
  } catch (error) {
    const message = error instanceof Error ? error.message : 'Unknown error'
    errorMessage.value = `Failed to load students. ${message}`
  } finally {
    isLoading.value = false
  }
}

const formatDate = (isoDate: string) => {
  const date = new Date(isoDate)
  if (Number.isNaN(date.getTime())) {
    return isoDate
  }
  return date.toLocaleDateString()
}

onMounted(loadStudents)
</script>

<template>
  <main class="page">
    <section class="card">
      <div class="header">
        <button class="primary" type="button">Create Student</button>
      </div>

      <div v-if="isLoading" class="status">Loading students...</div>
      <div v-else-if="errorMessage" class="status error">{{ errorMessage }}</div>

      <div v-else class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Id</th>
              <th>First Name</th>
              <th>Last Name</th>
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
              <td>{{ student.firstName }}</td>
              <td>{{ student.lastName }}</td>
              <td>{{ student.gender }}</td>
              <td>{{ formatDate(student.birthDate) }}</td>
              <td>{{ student.address ?? '-' }}</td>
              <td>
                <button class="ghost" type="button">Modify</button>
              </td>
              <td>
                <button class="danger" type="button">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</template>

<style scoped>
.page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 32px 16px;
  background: radial-gradient(circle at top, #f2f4ff, #f8fafc 45%, #f1f5f9 100%);
}

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

.primary {
  border: none;
  background: #0f172a;
  color: #ffffff;
  padding: 12px 20px;
  border-radius: 999px;
  font-weight: 600;
  cursor: pointer;
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

.ghost,
.danger {
  border: 1px solid #cbd5f5;
  padding: 6px 12px;
  border-radius: 8px;
  background: #ffffff;
  cursor: pointer;
}

.danger {
  border-color: #fecaca;
  color: #b91c1c;
}

.status {
  font-weight: 600;
}

.error {
  color: #b91c1c;
}
</style>
