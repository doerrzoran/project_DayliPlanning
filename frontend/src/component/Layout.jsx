import Header from "./header"


export default function Layout(props) {
    let {content} = props
    return(
        <>
            <Header/>
            <main>
                {
                    content
                }
            </main>
        </>
    )
}