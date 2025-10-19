import { create } from 'zustand';

export const apiStore = create((set) => ({
  apiUrl: 'https://127.0.0.1:8000/api',
  isConnected: false, // état indiquant si la connexion est établie
  statusMessage: '',  // message optionnel

  // méthode pour mettre à jour l'état de connexion et message
  setConnectionStatus: (connected, message = '') => set({ 
    isConnected: connected, 
    statusMessage: message 
  }),

  // Méthode pour obtenir l'URL complète de récupération du token
  getTokenUrl: () => {
    return `${apiStore.getState().apiUrl}/login_check`;
  },
  getMe: () => {
    return `${apiStore.getState().apiUrl}/me`;
  },
  getTag: () => {
    return `${apiStore.getState().apiUrl}/tag`;
  }
}));

export const userStore = create((set) => ({
  user: null,
  setUser: (userData) => set({ user: userData }),
  statut:'absent',
  setStatut: (statutData) => set({statut: statutData})
}));