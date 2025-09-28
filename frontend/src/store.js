import { create } from 'zustand';

export const apiStore = create((set) => ({
  apiUrl: 'http://127.0.0.1:8000/api',
  isConnected: false, // état indiquant si la connexion est établie
  statusMessage: '',  // message optionnel

  // méthode pour mettre à jour l'état de connexion et message
  setConnectionStatus: (connected, message = '') => set({ 
    isConnected: connected, 
    statusMessage: message 
  }),
}));
