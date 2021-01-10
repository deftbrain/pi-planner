import React from 'react';
import {
  Button,
  ChipField,
  Create,
  Datagrid,
  DeleteButton,
  Edit,
  EditButton,
  Error,
  List,
  Loading,
  NumberField,
  ReferenceArrayField,
  ReferenceField,
  ReferenceManyField,
  Show,
  ShowButton,
  SingleFieldList,
  Tab,
  TabbedShowLayout,
  TextField,
  useShowController,
} from 'react-admin'
import GroupAdd from "@material-ui/icons/GroupAdd";
import {Link} from 'react-router-dom';
import {ProjectSettingsForm} from './ProjectSettingsForm';

const ProjectSettingCreate = props => (
  <Create {...props}>
    <ProjectSettingsForm location={props.location}/>
  </Create>
);

const ProjectSettingEdit = props => (
  <Edit {...props}>
    <ProjectSettingsForm location={props.location}/>
  </Edit>
);

const ProjectSettingShow = props => {
  const {loading, error, record} = useShowController(props);

  if (loading) return <Loading/>;
  if (error) return <Error/>;
  if (!record) return null;

  return (
    <Show {...props}>
      <TabbedShowLayout>
        <Tab label="General">
          <ReferenceField source="programIncrement" reference="program_increments">
            <ChipField source="name"/>
          </ReferenceField>
          <ReferenceField source="project" reference="projects" link="">
            <ChipField source="name"/>
          </ReferenceField>
          <ReferenceManyField label="Epics" target="projectSettings" reference="epics">
            <SingleFieldList>
              <ChipField source="name"/>
            </SingleFieldList>
          </ReferenceManyField>
          <ReferenceArrayField source="sprints" reference="sprints">
            <SingleFieldList>
              <ChipField source="name"/>
            </SingleFieldList>
          </ReferenceArrayField>
          <ReferenceField source="defaultWorkitemStatus" reference="workitem_statuses" link="">
            <ChipField source="name"/>
          </ReferenceField>
        </Tab>
        <Tab label="Team capacity" path="capacity">
          <Button
            component={Link}
            to={{
              pathname: "/team_sprint_capacities/create",
              search: `?projectSettings=${record.id}`,
            }}
            label="Add team capacity"
          >
            <GroupAdd/>
          </Button>
          <ReferenceManyField
            addLabel={false}
            reference="team_sprint_capacities"
            target="projectSettings"
          >
            <Datagrid>
              <ReferenceField source="team" reference="teams" link="">
                <TextField source="name"/>
              </ReferenceField>
              <ReferenceField source="sprint" reference="sprints" link="">
                <TextField source="name"/>
              </ReferenceField>
              <NumberField source="capacity.frontend" label="Frontend capacity"/>
              <NumberField source="capacity.backend" label="Backend capacity"/>
              <EditButton/>
              <DeleteButton redirect={false}/>
            </Datagrid>
          </ReferenceManyField>
        </Tab>
        <Tab label="Team Epics" path="epics">
          <ReferenceManyField addLabel={false} target="projectSettings" reference="epics">
            <Datagrid>
              <TextField label="Epic" source="name"/>
              <ReferenceArrayField source="teams" reference="teams">
                <SingleFieldList>
                  <ChipField source="name"/>
                </SingleFieldList>
              </ReferenceArrayField>
              <EditButton/>
            </Datagrid>
          </ReferenceManyField>
        </Tab>
      </TabbedShowLayout>
    </Show>
  );
};

const ProjectSettingList = props => (
  <List {...props}>
    <Datagrid>
      <ReferenceField source="programIncrement" reference="program_increments">
        <TextField source="name"/>
      </ReferenceField>
      <ReferenceField source="project" reference="projects">
        <TextField source="name"/>
      </ReferenceField>
      <ShowButton/>
      <EditButton/>
    </Datagrid>
  </List>
);

export default {
  create: ProjectSettingCreate,
  edit: ProjectSettingEdit,
  show: ProjectSettingShow,
  list: ProjectSettingList,
};
