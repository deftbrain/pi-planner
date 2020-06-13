import React from 'react';
import Link from '@material-ui/core/Link';

export default function (props) {
  function stopPropagation(e) {
    e.stopPropagation();
  }

  return <Link href={props.entity['externalUrl']} target="_blank" onClick={stopPropagation} onFocus={stopPropagation}
               aria-label="External link">
    {props.entity['name']}
  </Link>
}
