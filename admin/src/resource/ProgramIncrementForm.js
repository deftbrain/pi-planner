import React from 'react';
import {ArrayInput, FormDataConsumer, SimpleForm, SimpleFormIterator, TextInput} from 'react-admin'
import {ProjectSettings} from './ProjectSettings';

export const ProgramIncrementForm = props => (
  <SimpleForm {...props} >
    <TextInput label="Name" source="name"/>
    <ArrayInput label="Projects" source="projectSettings">
      <SimpleFormIterator>
        <FormDataConsumer>
          {formDataProps => (
            <ProjectSettings {...formDataProps}/>
          )}
        </FormDataConsumer>
      </SimpleFormIterator>
    </ArrayInput>
  </SimpleForm>
);
