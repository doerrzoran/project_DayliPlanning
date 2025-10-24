import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const apiStore = create((set) => ({
  apiUrl: 'https://127.0.0.1:8000/api',
  isConnected: false, 
  statusMessage: '',  

  setConnectionStatus: (connected, message = '') => set({ 
    isConnected: connected, 
    statusMessage: message 
  }),


  getTokenUrl: () => {
    return `${apiStore.getState().apiUrl}/login_check`;
  },
  getMe: () => {
    return `${apiStore.getState().apiUrl}/me`;
  },
  getTag: () => {
    return `${apiStore.getState().apiUrl}/tag`;
  },
  getTypeAbsences: () => {
    return `${apiStore.getState().apiUrl}/absence_types`;
  },
  postAbsenceRequest : () => {
    return `${apiStore.getState().apiUrl}/absence/request`;
  },
  getTeam : () => {
    return `${apiStore.getState().apiUrl}/team`;
  },
  postValidateAbsence : () => {
    return `${apiStore.getState().apiUrl}/team/absence`;
  }
}));

export const userStore = create(
  persist(
    (set) => ({
      user: null,
      statut: 'absent',
      setUser: (userData) => set({ user: userData }),
      setStatut: (statutData) => set({ statut: statutData }),
    }),
    {
      name: 'user-storage',
    }
  )
);

export const teamStore = create(
  persist(
    (set) => ({
      team: [],
      setTeam: (teamData) => set({ team: teamData }),
    }),
    {
      name: 'team-storage',
      partialize: (state) => {
        const user = JSON.parse(localStorage.getItem('user-storage'))?.state?.user;
        if (user?.role === 'ROLE_CADRE') {
          return state;
        }
        return {}; 
      },
    }
  )
);