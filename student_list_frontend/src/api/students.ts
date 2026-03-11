import type { Student, StudentInput } from '../types/student'

const baseUrl = import.meta.env.VITE_API_BASE_URL ?? ''

const request = async <T>(path: string, options?: RequestInit): Promise<T> => {
  const response = await fetch(`${baseUrl}${path}`, options)
  if (!response.ok) {
    throw new Error(`Request failed: ${response.status}`)
  }
  return (await response.json()) as T
}

export const listStudents = async (): Promise<Student[]> => {
  return request<Student[]>('/students')
}

export const createStudent = async (input: StudentInput): Promise<Student> => {
  return request<Student>('/students', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(input),
  })
}

export const updateStudent = async (id: number, input: StudentInput): Promise<Student> => {
  return request<Student>(`/students/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id, ...input }),
  })
}

export const deleteStudent = async (id: number): Promise<void> => {
  const response = await fetch(`${baseUrl}/students/${id}`, { method: 'DELETE' })
  if (!response.ok && response.status !== 204) {
    throw new Error(`Request failed: ${response.status}`)
  }
}
