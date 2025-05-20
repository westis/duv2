"use client";

export default function RootError({ error }: { error: Error }) {
  return <div>Something went wrong: {error.message}</div>;
}
