import {Outlet} from 'react-router-dom';
import React    from 'react';
/**
 * PageLayout component to render the outlet for nested routes.
 */
export const PageLayout = function () {
    return <div><Outlet/></div>;
};
