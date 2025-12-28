function Card({ children, className = '' }) {
    return (
        <div className={`rounded-lg border border-gray-200 bg-white shadow-sm ${className}`}>
            {children}
        </div>
    );
}

function CardHeader({ children, className = '' }) {
    return (
        <div className={`flex flex-col gap-3 border-b border-gray-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between ${className}`}>
            {children}
        </div>
    );
}

function CardTitle({ children }) {
    return <h3 className="text-base font-semibold text-gray-900">{children}</h3>;
}

function CardContent({ children, className = '' }) {
    return <div className={`p-4 ${className}`}>{children}</div>;
}

Card.Header = CardHeader;
Card.Title = CardTitle;
Card.Content = CardContent;

export default Card;
