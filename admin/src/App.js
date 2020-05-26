import React from 'react';
import {HydraAdmin, ResourceGuesser} from '@api-platform/admin';
import {ProgramIncrementCreate, ProgramIncrementEdit, ProgramIncrementList} from './resource/program-increment';
import {TeamSprintCapacityCreate} from "./resource/team-sprint-capacity";

export default () => (
  <HydraAdmin entrypoint={process.env.REACT_APP_API_ENTRYPOINT}>
    <ResourceGuesser name="backlog_groups"/>
    <ResourceGuesser name="epics"/>
    <ResourceGuesser name="epic_statuses"/>
    <ResourceGuesser name="program_increments" list={ProgramIncrementList} create={ProgramIncrementCreate}
                     edit={ProgramIncrementEdit}/>
    <ResourceGuesser name="projects"/>
    <ResourceGuesser name="sprints"/>
    <ResourceGuesser name="sprint_schedules"/>
    <ResourceGuesser name="teams"/>
    <ResourceGuesser name="team_sprint_capacities" create={TeamSprintCapacityCreate}/>
    <ResourceGuesser name="workitems"/>
  </HydraAdmin>
);
