import React from 'react';
import {HydraAdmin, ResourceGuesser} from '@api-platform/admin';

export default () => (
  <HydraAdmin entrypoint={process.env.REACT_APP_API_ENTRYPOINT}>
    <ResourceGuesser name="program_increments"/>
    <ResourceGuesser name="projects"/>
  </HydraAdmin>
);
