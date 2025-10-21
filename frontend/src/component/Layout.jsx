import Header from "./header"
import Nav from "./nav"


export default function Layout(props) {
    let {content} = props
    return(
        <>
            <Header/>
            <main>
                <Nav/>
                {
                    content
                }
            </main>
        </>
    )
}