import React from 'react';
import {Redirect, Route} from 'react-router-dom';
import {
  fetchHydra as baseFetchHydra,
  HydraAdmin,
  hydraDataProvider as baseHydraDataProvider
} from '@api-platform/admin';
import {Resource} from 'react-admin';
import parseHydraDocumentation from "@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation";
import {ProgramIncrementList} from './resource/ProgramIncrementList';
import {ProgramIncrementCreate} from './resource/ProgramIncremenCreate';
import {ProgramIncrementEdit} from './resource/ProgramIncremenEdit';
import authProvider, {AUTHENTICATION_SCHEME, getToken, isTokenValid} from './authProvider';
import LoginPage from './Login';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;
const fetchHeaders = isTokenValid() ? {Authorization: [AUTHENTICATION_SCHEME, getToken()].join(' ')} : {};
const fetchHydra = (url, options = {}) => baseFetchHydra(url, {
  ...options,
  headers: new Headers(fetchHeaders),
});
const apiDocumentationParser = entrypoint => parseHydraDocumentation(entrypoint, {headers: new Headers(fetchHeaders)})
  .then(
    ({api}) => ({api}),
    (result) => {
      switch (result.status) {
        case 401:
          return Promise.resolve({
            api: result.api,
            customRoutes: [
              <Route path="/" render={() => {
                return isTokenValid() ? window.location.reload() : <Redirect to="/login"/>;
              }}/>
            ],
          });

        default:
          return Promise.reject(result);
      }
    },
  );
const dataProvider = baseHydraDataProvider(entrypoint, fetchHydra, apiDocumentationParser);
export default () => (
  <HydraAdmin entrypoint={entrypoint} dataProvider={dataProvider} authProvider={authProvider}
              loginPage={LoginPage}>
    <Resource name="program_increments" list={ProgramIncrementList} create={ProgramIncrementCreate}
              edit={ProgramIncrementEdit}/>
    <Resource name="backlog_groups"/>
    <Resource name="epics"/>
    <Resource name="epic_statuses"/>
    <Resource name="projects"/>
    <Resource name="sprints"/>
    <Resource name="sprint_schedules"/>
    <Resource name="teams"/>
    <Resource name="workitems"/>
  </HydraAdmin>
);
