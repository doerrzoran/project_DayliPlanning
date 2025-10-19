import './App.css'
import {  useMemo } from 'react'
import { createBrowserRouter, Navigate, RouterProvider } from 'react-router';
import Layout from './component/Layout';
import Calendar from './component/calendar'; 
import Login from './component/login';
import Tag from './component/Tag ';

function App() {
  const router = useMemo(() => {
    
    return createBrowserRouter([
      {
        path: '/',
        element: <Tag to="/calendar" replace /> // redirige par défaut vers /calendar
      },
      {
        path: '/',
        element: <Navigate to="/calendar" /> // redirige par défaut vers /calendar
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
