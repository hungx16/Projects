export type Student = {
  id: number
  firstName: string
  lastName: string
  gender: string
  birthDate: string
  address: string | null
  fullName: string
  age: number
}

export type StudentInput = {
  firstName: string
  lastName: string
  gender: string
  birthDate: string
  address: string | null
}
