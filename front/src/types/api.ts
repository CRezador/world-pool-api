export interface ApiResponse<T> {
  message: string
  data: T
}

export interface ApiListResponse<T> {
  message: string
  data: T[]
}

export interface ApiPaginatedResponse<T> {
  message: string
  data: T[]
  meta: {
    current_page: number
    per_page: number
    total: number
    last_page: number
  }
}
