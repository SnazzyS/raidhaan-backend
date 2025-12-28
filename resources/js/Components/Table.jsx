function Table({ children }) {
    return (
        <div className="overflow-x-auto">
            <table className="w-full text-sm text-gray-700">
                {children}
            </table>
        </div>
    );
}

function TableHead({ children }) {
    return (
        <thead className="border-y border-gray-200 bg-gray-50">
            <tr>{children}</tr>
        </thead>
    );
}

function TableHeadCell({ children, className = '' }) {
    return (
        <th className={`px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider ${className}`}>
            {children}
        </th>
    );
}

function TableBody({ children }) {
    return <tbody className="divide-y divide-gray-200">{children}</tbody>;
}

function TableRow({ children }) {
    return <tr className="hover:bg-gray-50">{children}</tr>;
}

function TableCell({ children, className = '' }) {
    return <td className={`px-4 py-3 text-gray-700 ${className}`}>{children}</td>;
}

Table.Head = TableHead;
Table.HeadCell = TableHeadCell;
Table.Body = TableBody;
Table.Row = TableRow;
Table.Cell = TableCell;

export default Table;
