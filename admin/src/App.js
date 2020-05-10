import React from 'react';
import {HydraAdmin, ResourceGuesser} from '@api-platform/admin';

export default () => (
  <HydraAdmin entrypoint={process.env.REACT_APP_API_ENTRYPOINT}>
    <ResourceGuesser name="backlog_groups"/>
    <ResourceGuesser name="epics"/>
    <ResourceGuesser name="epic_statuses"/>
    <ResourceGuesser name="program_increments"/>
    <ResourceGuesser name="projects"/>
    <ResourceGuesser name="sprints"/>
    <ResourceGuesser name="sprint_schedules"/>
    <ResourceGuesser name="teams"/>
    <ResourceGuesser name="team_sprint_capacities"/>
    <ResourceGuesser name="workitems"/>
  </HydraAdmin>
);
