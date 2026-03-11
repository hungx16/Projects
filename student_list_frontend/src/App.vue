<script setup lang="ts">
import { onMounted, ref } from 'vue'
import StudentList from './components/student_list.vue'
import StudentDialog from './components/student_dialog.vue'
import { createStudent, deleteStudent, listStudents, updateStudent } from './api/students'
import type { Student, StudentInput } from './types/student'

const students = ref<Student[]>([])
const isLoading = ref(true)
const errorMessage = ref('')
const isSubmitting = ref(false)
const isDialogOpen = ref(false)
const dialogMode = ref<'create' | 'edit' | 'delete'>('create')
const activeStudent = ref<Student | null>(null)

const loadStudents = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    students.value = await listStudents()
  } catch (error) {
    const message = error instanceof Error ? error.message : 'Unknown error'
    errorMessage.value = `Failed to load students. ${message}`
  } finally {
    isLoading.value = false
  }
}

const openCreate = () => {
  dialogMode.value = 'create'
  activeStudent.value = null
  isDialogOpen.value = true
}

const openEdit = (student: Student) => {
  dialogMode.value = 'edit'
  activeStudent.value = student
  isDialogOpen.value = true
}

const openDelete = (student: Student) => {
  dialogMode.value = 'delete'
  activeStudent.value = student
  isDialogOpen.value = true
}

const closeDialog = () => {
  isDialogOpen.value = false
}

const handleSubmit = async (payload: {
  mode: 'create' | 'edit' | 'delete'
  data?: StudentInput
  id?: number
}) => {
  isSubmitting.value = true
  errorMessage.value = ''
  try {
    if (payload.mode === 'create' && payload.data) {
      await createStudent(payload.data)
    } else if (payload.mode === 'edit' && payload.data && payload.id !== undefined) {
      await updateStudent(payload.id, payload.data)
    } else if (payload.mode === 'delete' && payload.id !== undefined) {
      await deleteStudent(payload.id)
    }
    await loadStudents()
    closeDialog()
  } catch (error) {
    const message = error instanceof Error ? error.message : 'Unknown error'
    errorMessage.value = `Request failed. ${message}`
  } finally {
    isSubmitting.value = false
  }
}

onMounted(loadStudents)
</script>

<template>
  <main class="page">
    <StudentList
      :students="students"
      :is-loading="isLoading"
      :error-message="errorMessage"
      @create="openCreate"
      @edit="openEdit"
      @delete="openDelete"
    />

    <StudentDialog
      :is-open="isDialogOpen"
      :mode="dialogMode"
      :student="activeStudent"
      :is-submitting="isSubmitting"
      @close="closeDialog"
      @submit="handleSubmit"
    />
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

:deep(.primary) {
  border: none;
  background: #0f172a;
  color: #ffffff;
  padding: 12px 20px;
  border-radius: 999px;
  font-weight: 600;
  cursor: pointer;
}

:deep(.ghost),
:deep(.danger) {
  border: 1px solid #cbd5f5;
  padding: 6px 12px;
  border-radius: 8px;
  background: #ffffff;
  cursor: pointer;
}

:deep(.danger) {
  border-color: #fecaca;
  color: #b91c1c;
}
</style>
