export type PoolMemberRole = 'OWNER' | 'ADMIN' | 'MEMBER'
export type PoolMemberStatus = 'ACTIVE' | 'LEFT' | 'BANNED'

export interface Pool {
  id: number
  name: string
  join_code: string
  is_public: boolean
  owner: string
}

export interface PoolMember {
  id: number
  user_id: number
  user_name: string
  role: PoolMemberRole
  status: PoolMemberStatus
  joined_at: string
}
