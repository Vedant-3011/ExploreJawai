( function () {
	const buttons = document.querySelectorAll( '.ej-blog-filter[data-filter]' );
	const cards = document.querySelectorAll( '.ej-blog-archive-grid [data-category]' );
	if ( ! buttons.length || ! cards.length ) {
		return;
	}
	buttons.forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			buttons.forEach( ( b ) => b.classList.remove( 'is-active' ) );
			btn.classList.add( 'is-active' );
			const key = btn.dataset.filter;
			cards.forEach( ( card ) => {
				const cat = card.getAttribute( 'data-category' ) || '';
				card.hidden = key !== 'all' && cat !== key;
			} );
		} );
	} );
} )();