import React from 'react';

export default function ({value = null, ...restProps}) {
  return value && (
    <span {...restProps}>
      <abbr title="Weighted Shortest Job First">WSJF</abbr>: {value.toFixed(2)}
    </span>
  );
}
