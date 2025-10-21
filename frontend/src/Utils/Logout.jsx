import { useNavigate } from "react-router";

export default function(){
    const navigate = useNavigate();

    localStorage.removeItem('authToken')
    navigate('/')
}