export type UserRole = 'ADMIN' | 'USER'

export interface User {
  name: string
  email: string
  role?: UserRole
  created_at: string
  updated_at: string
}
