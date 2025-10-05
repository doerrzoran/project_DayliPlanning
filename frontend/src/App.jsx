import './App.css'
import {  useMemo } from 'react'
import { createBrowserRouter, RouterProvider } from 'react-router';
import TestApi from './component/TestApi'
import Calendar from './component/calendar'; 

function App() {
  const router = useMemo(() => {
    return createBrowserRouter([
      {
        path: '/test',
        element: <TestApi></TestApi>
      },
      {
        path: '/calendar',
        element: <Calendar></Calendar>
      }
    ])
  })
  return (
    <>
        <RouterProvider router={router} />
    </>
  )
}

export default App
