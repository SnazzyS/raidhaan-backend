export default function StatCard({ title, value, icon: Icon }) {
    return (
        <div className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm font-medium text-gray-500">{title}</p>
                    <p className="mt-1 text-2xl font-semibold text-gray-900">{value}</p>
                </div>
                {Icon && (
                    <div className="rounded-lg bg-gray-100 p-2">
                        <Icon className="h-5 w-5 text-gray-600" />
                    </div>
                )}
            </div>
        </div>
    );
}
