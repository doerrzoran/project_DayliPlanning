import {  useMemo } from 'react'
import { createBrowserRouter, RouterProvider } from 'react-router';
import TestApi from './component/TestApi'

import './App.css'

function App() {
  const router = useMemo(() => {
    return createBrowserRouter([
      {
        path: '/test',
        element: <TestApi></TestApi>
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
