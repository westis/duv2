import React from "react";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  // TODO: Add authentication check
  return <div>Admin Layout {children}</div>;
}
