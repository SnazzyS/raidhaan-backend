export default function Select({
    label,
    options = [],
    placeholder,
    error,
    className = '',
    ...props
}) {
    return (
        <div className={className}>
            {label && (
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    {label}
                </label>
            )}
            <select
                className={`
                    w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
                    focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200
                    ${error ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100' : ''}
                `}
                {...props}
            >
                {placeholder && <option value="">{placeholder}</option>}
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
            {error && (
                <p className="mt-1 text-xs text-rose-600">{error}</p>
            )}
        </div>
    );
}
