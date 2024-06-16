import React                          from 'react';
import ReactDOM                       from 'react-dom/client';
import {BrowserRouter, Route, Routes} from 'react-router-dom';
import {PageLayout}                   from './PageLayout';
import {PageHome}                     from './PageHome';
const App = () => {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<PageLayout/>}>
                    <Route index element={<PageHome/>}/>
                </Route>
            </Routes>
        </BrowserRouter>
    );
};
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App/>);
