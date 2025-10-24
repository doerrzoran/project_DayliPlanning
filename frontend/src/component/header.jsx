import Login from "./login";
import logo from '../assets/image/logo.png'
import '../styles/Header.css';
import GetUser from "./GetUser";

export default function Header() {

    return(
        <>
            <header>
                <div id='logo'>
                    <img className='logo' src={logo} alt="logo" />
                </div>
                <div>
                    <GetUser/>
                </div>
            </header>   
        </>
    )  
}