import React                          from 'react';
import ReactDOM                       from 'react-dom/client';
import {BrowserRouter, Route, Routes} from 'react-router-dom';
import {PageLayout}                   from './PageLayout';
import {PageHome}                     from './PageHome';
import {PageMatchGet}                 from './PageMatchGet';
import {PageMatchCheck}               from './PageMatchCheck';

/**
 * Main application component.
 * Sets up the routing for the application using React Router.
 */
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

// Create and render the root element.
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App/>);
