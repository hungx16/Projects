<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import type { Student, StudentInput } from '../types/student'

type DialogMode = 'create' | 'edit' | 'delete'

type DialogPayload =
  | { mode: 'create'; data: StudentInput }
  | { mode: 'edit'; id: number; data: StudentInput }
  | { mode: 'delete'; id: number }

type Props = {
  isOpen: boolean
  mode: DialogMode
  student: Student | null
  isSubmitting: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submit', payload: DialogPayload): void
}>()

const formState = ref<StudentInput>({
  firstName: '',
  lastName: '',
  gender: '',
  birthDate: '',
  address: null,
})

const isFormMode = computed(() => props.mode === 'create' || props.mode === 'edit')

const canSubmit = computed(() => {
  if (!isFormMode.value) {
    return true
  }
  return (
    formState.value.firstName.trim().length > 0 &&
    formState.value.lastName.trim().length > 0 &&
    formState.value.gender.trim().length > 0 &&
    formState.value.birthDate.trim().length > 0
  )
})

const modalTitle = computed(() => {
  if (props.mode === 'edit') {
    return 'Modify Student'
  }
  if (props.mode === 'delete') {
    return 'Delete Student'
  }
  return 'Create Student'
})

const submitLabel = computed(() => {
  if (props.mode === 'edit') {
    return 'Save changes'
  }
  if (props.mode === 'delete') {
    return 'Delete'
  }
  return 'Create'
})

const resetForm = () => {
  formState.value = {
    firstName: '',
    lastName: '',
    gender: '',
    birthDate: '',
    address: null,
  }
}

watch(
  () => [props.isOpen, props.mode, props.student] as const,
  () => {
    if (!props.isOpen) {
      return
    }
    if (props.mode === 'edit' && props.student) {
      formState.value = {
        firstName: props.student.firstName ?? '',
        lastName: props.student.lastName ?? '',
        gender: props.student.gender ?? '',
        birthDate: props.student.birthDate ? props.student.birthDate.slice(0, 10) : '',
        address: props.student.address ?? null,
      }
      return
    }
    resetForm()
  },
)

const submitForm = () => {
  if (!canSubmit.value) {
    return
  }
  if (props.mode === 'delete') {
    if (props.student) {
      emit('submit', { mode: 'delete', id: props.student.id })
    }
    return
  }
  if (props.mode === 'edit') {
    if (!props.student) {
      return
    }
    emit('submit', {
      mode: 'edit',
      id: props.student.id,
      data: {
        firstName: formState.value.firstName.trim(),
        lastName: formState.value.lastName.trim(),
        gender: formState.value.gender.trim(),
        birthDate: formState.value.birthDate,
        address: formState.value.address ? formState.value.address.trim() : null,
      },
    })
    return
  }
  emit('submit', {
    mode: 'create',
    data: {
      firstName: formState.value.firstName.trim(),
      lastName: formState.value.lastName.trim(),
      gender: formState.value.gender.trim(),
      birthDate: formState.value.birthDate,
      address: formState.value.address ? formState.value.address.trim() : null,
    },
  })
}
</script>

<template>
  <div v-if="isOpen" class="backdrop" @click.self="emit('close')">
    <form class="modal" @submit.prevent="submitForm">
      <div class="modal-header">
        <h2>{{ modalTitle }}</h2>
        <button class="icon" type="button" @click="emit('close')">X</button>
      </div>

      <div v-if="mode === 'delete'" class="modal-body">
        <p>
          Delete {{ student?.firstName }} {{ student?.lastName }}? This cannot be undone.
        </p>
      </div>

      <div v-else class="modal-body">
        <label>
          First name
          <input v-model="formState.firstName" type="text" autocomplete="given-name" />
        </label>
        <label>
          Last name
          <input v-model="formState.lastName" type="text" autocomplete="family-name" />
        </label>
        <label>
          Gender
          <input v-model="formState.gender" type="text" />
        </label>
        <label>
          Birth date
          <input v-model="formState.birthDate" type="date" />
        </label>
        <label>
          Address
          <input v-model="formState.address" type="text" autocomplete="street-address" />
        </label>
      </div>

      <div class="modal-actions">
        <button class="ghost" type="button" @click="emit('close')">Cancel</button>
        <button
          :class="mode === 'delete' ? 'danger' : 'primary'"
          type="submit"
          :disabled="!canSubmit || isSubmitting"
        >
          {{ isSubmitting ? 'Saving...' : submitLabel }}
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: grid;
  place-items: center;
  padding: 24px;
}

.modal {
  width: min(520px, 100%);
  background: #ffffff;
  border-radius: 16px;
  padding: 20px 22px;
  display: grid;
  gap: 16px;
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-body {
  display: grid;
  gap: 12px;
}

label {
  display: grid;
  gap: 6px;
  font-weight: 600;
  color: #0f172a;
}

input {
  border: 1px solid #cbd5f5;
  border-radius: 8px;
  padding: 8px 10px;
  font: inherit;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.icon {
  border: none;
  background: transparent;
  font-size: 18px;
  cursor: pointer;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
