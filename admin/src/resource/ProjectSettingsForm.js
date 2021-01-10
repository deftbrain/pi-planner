import React from 'react';
import {
  AutocompleteInput,
  FormDataConsumer,
  ReferenceArrayInput,
  ReferenceInput,
  SelectArrayInput,
  SelectInput,
  SimpleForm,
} from 'react-admin'
import {ProjectSprintArrayInput} from './ProjectSprintArrayInput';
import {useForm} from 'react-final-form';

export const ProjectSettingsForm = props => {
  return (
    <SimpleForm {...props} redirect="show">
      <GeneralFormTab label="General"/>
    </SimpleForm>
  );
}

const GeneralFormTab = ({label, ...props}) => {

  const form = useForm();
  const resetProjectDependentInputs = () => {
    form.change('epics', null);
    form.change('sprints', null);
  }

  return (
    <>
      <ReferenceInput {...props} source="programIncrement" reference="program_increments">
        <SelectInput/>
      </ReferenceInput>
      <ReferenceInput {...props} source="project" reference="projects" onChange={resetProjectDependentInputs}>
        <AutocompleteInput/>
      </ReferenceInput>
      <FormDataConsumer>
        {({formData}) => formData.project &&
          <ReferenceArrayInput {...props} label="Epics" source="epics" reference="epics"
                               filter={{project: formData.project}}>
            <SelectArrayInput/>
          </ReferenceArrayInput>
        }
      </FormDataConsumer>
      <FormDataConsumer>
        {({formData}) => formData.project &&
          <ProjectSprintArrayInput {...props} label="Sprints" source="sprints" reference="sprints"
                                   project={formData.project}/>
        }
      </FormDataConsumer>
      <ReferenceInput {...props} source="defaultWorkitemStatus" reference="workitem_statuses">
        <AutocompleteInput/>
      </ReferenceInput>
    </>
  );
};
