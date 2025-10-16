import Login from "./login";
import '../styles/Header.css';
import GetUser from "./GetUser";

export default function Header() {

    return(
        <>
            <header>
                <div>
                    <GetUser/>
                </div>
            </header>   
        </>
    )  
}