import React from 'react';

export default function ({teams = [], ...restProps}) {
  return teams && (
    <span {...restProps}>
      {teams.map(t => t.name).join(', ')}
    </span>
  );
}
