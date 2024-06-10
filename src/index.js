import React                          from 'react';
import ReactDOM                       from 'react-dom/client';
import {BrowserRouter, Route, Routes} from 'react-router-dom';
import {PageLayout}                   from './PageLayout';
import {PageHome}                     from './PageHome';
import {PageMatchGet}                 from './PageMatchGet';
import {PageMatchCheck}               from './PageMatchCheck';
const App  = () => {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<PageLayout/>}>
                    <Route index element={<PageHome/>}/>
                    <Route path="/page-match-get" element={<PageMatchGet/>}/>
                    <Route path="/page-match-check" element={<PageMatchCheck/>}/>
                </Route>
            </Routes>
        </BrowserRouter>
    );
};
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App/>);
