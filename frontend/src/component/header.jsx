import Login from "./login";
import logo from '../assets/image/logo.png'
import '../styles/Header.css';
import GetUser from "./GetUser";

export default function Header() {

    return(
        <>
            <header>
                <div id='logo'>
                    <img className='logo' src={logo} alt="logo of Zilkor" />
                </div>
                <div>
                    <GetUser/>
                </div>
            </header>   
        </>
    )  
}