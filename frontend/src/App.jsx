import './App.css'
import {  useMemo } from 'react'
import { createBrowserRouter, Navigate, RouterProvider } from 'react-router';
import Layout from './component/Layout';
import Calendar from './component/calendar'; 
import Login from './component/login';
import Tag from './component/Tag';
import AbsenceRequest from './component/AbsenceRequest';

function App() {
  const router = useMemo(() => {
    
    return createBrowserRouter([
      {
        path: '/',
        element: <Login to="/tag" replace /> 
      },
      {
        path: '/tag',
        element: <Layout content = {<Tag/>} />
      },
      {
        path: '/calendar',
        element:  <Layout content = {<Calendar/>} />
      },
      {
        path: '/demande/absence',
        element:  <Layout content = {<AbsenceRequest/>} />
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
