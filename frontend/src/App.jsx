import './App.css'
import {  useMemo } from 'react'
import { createBrowserRouter, Navigate, RouterProvider } from 'react-router';
import Layout from './component/Layout';
import Calendar from './component/calendar'; 
import Login from './component/login';

function App() {
  const router = useMemo(() => {
    
    return createBrowserRouter([
      {
        path: '/',
        element: <Navigate to="/calendar" replace /> // redirige par défaut vers /calendar
      },
      {
        path: '/login',
        element: <Login/>
      },
      {
        path: '/calendar',
        element:  <Layout content = {<Calendar/>} />
      },
    ])
  }, [])
  return (
    <>
        <RouterProvider router={router} />
    </>
  )
}

export default App
