// import React from 'react';
// import {Error, Loading, ReferenceArrayInput, SelectArrayInput, useQueryWithStore} from 'react-admin';
//
// const Component = props => {
//   const {loading, error, data} = useQueryWithStore({
//     type: 'getMany',
//     resource: 'project_settings',
//     payload: {ids: props.record.projectsSettings}
//   });
//
//   if (loading) return <Loading/>;
//   if (error) return <Error/>;
//   if (!data) return null;
//
//   return (
//     <ReferenceArrayInput {...props} filter={{project: data.map(ps => ps.project)}}>
//       <SelectArrayInput/>
//     </ReferenceArrayInput>
//   );
// };
//
// export default Component;
